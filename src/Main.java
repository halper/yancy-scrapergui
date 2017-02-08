import com.sun.org.apache.xerces.internal.impl.io.MalformedByteSequenceException;
import org.apache.log4j.Logger;
import org.xml.sax.SAXException;
import twitter4j.Twitter;
import twitter4j.TwitterException;
import utilities.ConnectionHandler;

import javax.net.ssl.SSLException;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.SimpleDateFormat;
import java.util.*;
import java.util.concurrent.*;

/**
 * Created by alper on 1/10/17.
 */
public class Main {
    private static HashMap<Long, Product> mainMapOfProducts;
    private static HashMap<String, ProductURL> productUrlsMap;
    private static final int TIMEOUT_SECS_FOR_XML_PARSING = 15;
    private static List<String> siteURLs;
    private static List<String> filters;
    private static boolean init;
    private static final Logger logger = Logger.getLogger( Main.class );

    public static void main(String[] args) {
        ExecutorService executorService = Executors.newFixedThreadPool(500);
        logger.info("System initialized");
        init = false;
        while (true) {
            initFromDB(executorService);
            checkForModifiedProductURLsMap(executorService);
            System.gc();
        }
    }

    private static void initFromDB(ExecutorService executorService) {
        setSiteUrlsFromDB();
        setFiltersFromDB();
        setProductUrlsFromDB();
        if (!init) {
            init = true;
            List<String> productUrls = new ArrayList<>(productUrlsMap.keySet());
            mainMapOfProducts = getProducts(executorService, productUrls);
        }
    }

    private static void setProductUrlsFromDB() {
        productUrlsMap = new HashMap<>();

        String sql = "SELECT * FROM producturls";
        try (Connection connection = DataSource.getInstance().getBds().getConnection();
             PreparedStatement statement = connection.prepareStatement(sql);
             ResultSet rs = statement.executeQuery()) {
            while (rs.next()) {
                ProductURL pUrl = new ProductURL();
                pUrl.setId(rs.getLong("id"));
                pUrl.setUrl(rs.getString("url"));
                pUrl.setLastMod(rs.getString("lastMod"));
                String imageLoc = rs.getString("imageLoc");
                if (!imageLoc.toLowerCase().matches("null"))
                    pUrl.setImageLoc(imageLoc);
                productUrlsMap.put(pUrl.getUrlString(), pUrl);
            }
        } catch (SQLException e) {
            logger.error("SQL Error!\n", e);
        }
    }

    private static void setFiltersFromDB() {
        filters = new ArrayList<>();

        String sql = "SELECT keyword FROM filters";
        try (Connection connection = DataSource.getInstance().getBds().getConnection();
             PreparedStatement statement = connection.prepareStatement(sql);
             ResultSet rs = statement.executeQuery()) {
            while (rs.next()) {
                filters.add(rs.getString("keyword"));
            }
        } catch (SQLException e) {
            logger.error("SQL Error!\n", e);
        }
    }

    private static void setSiteUrlsFromDB() {
        siteURLs = new ArrayList<>();

        String sql = "SELECT url FROM sites";
        try (Connection connection = DataSource.getInstance().getBds().getConnection();
             PreparedStatement statement = connection.prepareStatement(sql);
             ResultSet rs = statement.executeQuery()) {
            while (rs.next()) {
                siteURLs.add(rs.getString("url"));
            }
        } catch (SQLException e) {
            logger.error("SQL Error!\n", e);
        }
    }

    private static void checkForModifiedProductURLsMap(ExecutorService executorService) {
        HashMap<String, ProductURL> compareMap = getProductUrls(executorService);
        List<String> newProducts = new ArrayList<>();
        List<String> modifiedProducts = new ArrayList<>();

        for (String s : compareMap.keySet()) {
            if (!productUrlsMap.containsKey(s)) {
                newProducts.add(s);
            } else if (!productUrlsMap.get(s).getLastMod().matches(compareMap.get(s).getLastMod())) {
                modifiedProducts.add(s);
            }
        }
        if (newProducts.size() > 0 || modifiedProducts.size() > 0) {
            if (newProducts.size() > 0) {
                insertNewProductsToDB(newProducts, compareMap);
                sendNotificationsAboutNewProductsAndPutThemIntoMap(executorService, newProducts);
            }
            if (modifiedProducts.size() > 0) {
                updateModifiedProductsInDB(modifiedProducts, compareMap);
                checkForModifiedProductsNotifyAndUpdateProductsMap(executorService, modifiedProducts);
            }
        }
    }

    private static void updateModifiedProductsInDB(List<String> modifiedProducts, HashMap<String, ProductURL> newProductURLMap) {
        String sql = "UPDATE producturls SET imageLoc=?, lastMod=?, updated_at=? WHERE id=?";
        try (Connection connection = DataSource.getInstance().getBds().getConnection();
             PreparedStatement statement = connection.prepareStatement(sql)) {
            int i = 0;
            for (String s : modifiedProducts) {
                ProductURL newProductUrl = newProductURLMap.get(s);
                ProductURL oldProductUrl = productUrlsMap.get(s);

                SimpleDateFormat sdf = new SimpleDateFormat("YYYY-MM-dd HH:mm:ss");
                String date = sdf.format(new Date());
                statement.setString(1, newProductUrl.getImageLoc() == null ? "NULL" : newProductUrl.getImageLoc().toString());
                statement.setString(2, newProductUrl.getLastMod());
                statement.setString(3, date);
                statement.setLong(4, oldProductUrl.getId());
                statement.addBatch();
                if (i != 0 && i % 1000 == 0)
                    statement.executeBatch();
                i++;
            }
            statement.executeBatch();
        } catch (SQLException e) {
            logger.error("SQL Error!\n", e);
        }
    }

    private static void insertNewProductsToDB(List<String> newProducts, HashMap<String, ProductURL> newProductURLMap) {

        String sql = "INSERT INTO producturls (url, imageLoc, lastMod, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
        try (
                Connection connection = DataSource.getInstance().getBds().getConnection();

                PreparedStatement statement = connection.prepareStatement(sql)) {
            int i = 0;
            for (String s : newProducts) {
                ProductURL pUrl = newProductURLMap.get(s);
                statement.setString(1, pUrl.getUrlString());
                SimpleDateFormat sdf = new SimpleDateFormat("YYYY-MM-dd HH:mm:ss");
                String date = sdf.format(new Date());
                statement.setString(2, pUrl.getImageLoc() == null ? "NULL" : pUrl.getImageLoc().toString());
                statement.setString(3, pUrl.getLastMod());
                statement.setString(4, date);
                statement.setString(5, date);
                statement.addBatch();
                if (i != 0 && i % 1000 == 0)
                    statement.executeBatch();
                i++;
            }
            statement.executeBatch();
        } catch (SQLException e) {
            logger.error("SQL Error!\n", e);
        }
    }

    private static void checkForModifiedProductsNotifyAndUpdateProductsMap(ExecutorService executorService, List<String> modifiedProducts) {
        HashMap<Long, Product> modifiedMap = getProducts(executorService, modifiedProducts);
        Runnable task = () -> {
            for (Long aLong : modifiedMap.keySet()) {
                String modifiedText = null;
                Product oldProduct = mainMapOfProducts.get(aLong);
                Product newProduct = modifiedMap.get(aLong);
                if (newProduct.getQuantity() != 0 && oldProduct.getPrice() > newProduct.getPrice()) {
                    modifiedText = "Reduced price ";
                }
                if (newProduct.getQuantity() > 0 && oldProduct.getPrice() == 0 && newProduct.getPrice() > 0) {
                    modifiedText = "Restocked ";
                }
                if(modifiedText != null){
                    sendTweet(newProduct, modifiedText);
                    sendModifiedProductMail(newProduct, modifiedText);
                }
                mainMapOfProducts.put(aLong, newProduct);
            }
        };
        Thread worker = new Thread(task);
        executorService.execute(worker);
    }

    private static void sendModifiedProductMail(Product product, String modification) {
        GMailSSL gs = new GMailSSL();
        gs.sendModifiedProductMail(product, modification);
    }

    private static void sendNotificationsAboutNewProductsAndPutThemIntoMap(ExecutorService executorService, List<String> newProducts) {
        Runnable task = () -> {
            HashMap<Long, Product> newProductsMap = getProducts(executorService, newProducts);
            int i = 0;
            for (Product product : newProductsMap.values()) {
                sendTweet(product, "New product ");
                sendNewProductMail(product);
                if (++i % 10 == 0) {
                    try {
                        Thread.sleep(60000);
                    } catch (InterruptedException e) {
                        logger.error("Interrupted method 'sendNotificationsAboutNewProductsAndPutThemIntoMap'!\n", e);
                    }
                }
            }
            mainMapOfProducts.putAll(newProductsMap);
        };
        Thread worker = new Thread(task);
        executorService.execute(worker);
    }

    private static void sendNewProductMail(Product product) {
        GMailSSL gs = new GMailSSL();
        gs.sendNewProductMail(product);
    }

    private static HashMap<Long, Product> getProducts(ExecutorService executorService, List<String> productUrls) {
        Long start = System.currentTimeMillis();
        logger.info("Parsing products!");

        HashMap<Long, Product> productsMap = new HashMap<>();

        List<Future<Product>> products = new ArrayList<>();
        for (String url : productUrls) {
            ProductURL productUrl = new ProductURL();
            productUrl.setUrl(url);
            Future<Product> future = executorService.submit(new ProductXMLParser(productUrl));
            products.add(future);
        }
        for (int i = 0; i < products.size(); i++) {
            Future<Product> product = products.get(i);
            try {
                Product myProduct = product.get(TIMEOUT_SECS_FOR_XML_PARSING, TimeUnit.SECONDS);
                myProduct.setProductURL(productUrlsMap.get(productUrls.get(i)));
                productsMap.put(myProduct.getId(), myProduct);
            } catch (InterruptedException | ExecutionException | NullPointerException e) {
                if (e instanceof NullPointerException)
                    logger.warn("NullPointerException for " + productUrls.get(i));
            } catch (TimeoutException e) {
                logger.warn("Timeout exception for " + productUrls.get(i));
            }
        }

        logger.info(productsMap.size() + " products parsed in " + (System.currentTimeMillis() - start) / 1000 + " seconds");
        return productsMap;
    }

    private static void sendTweet(Product myProduct, String statusText) {
        statusText = statusText + myProduct.getUrl() + " - " + myProduct.getTitle();
        Twitter twitter = TwitterFactorySource.getFactory().getInstance();
        try {
            twitter.updateStatus(statusText);
        } catch (TwitterException e) {
            logger.error("Twitter exception!", e);
        }
        logger.info(statusText);
    }

    private static HashMap<String, ProductURL> getProductUrls(ExecutorService executorService) {
        logger.info("Parsing product URLs!");
        Long start = System.currentTimeMillis();
        List<Future<HashMap<String, ProductURL>>> productsFutureList = new ArrayList<>();

        for (String url : siteURLs) {
            Future<HashMap<String, ProductURL>> future = executorService.submit(new URLWorker(url));
            productsFutureList.add(future);
        }
        HashMap<String, ProductURL> productUrlsAndLastModsMap = new HashMap<>();
        for (int i = 0; i < productsFutureList.size(); i++) {
            Future<HashMap<String, ProductURL>> listFuture = productsFutureList.get(i);
            try {
                HashMap<String, ProductURL> productURLHashMap = listFuture.get(TIMEOUT_SECS_FOR_XML_PARSING, TimeUnit.SECONDS);
                if (productURLHashMap != null)
                    productUrlsAndLastModsMap.putAll(productURLHashMap);
            } catch (InterruptedException | ExecutionException e) {
                logger.error("Error!", e);
            } catch (TimeoutException e) {
                logger.warn("Cannot get a URL in " + TIMEOUT_SECS_FOR_XML_PARSING + " seconds! " + siteURLs.get(i));
            } catch (NullPointerException n) {
                logger.warn("NullPointerException for " + siteURLs.get(i));
            }
        }
        logger.info("Product URLs parsed in " + (System.currentTimeMillis() - start) / 1000 + " seconds");
        return productUrlsAndLastModsMap;
    }

    private static class URLWorker implements Callable {
        private URL url;

        URLWorker(String url) {
            try {
                this.url = new URL(url + "/sitemap_products_1.xml");
            } catch (MalformedURLException e) {
                logger.error("Malformed URL! " + url, e);
            }
        }

        @Override
        public HashMap<String, ProductURL> call() throws Exception {
            HashMap<String, ProductURL> productUrlsAndLastMod = null;

            ConnectionHandler connectionHandler = new ConnectionHandler(url);
            HttpURLConnection connection = connectionHandler.getConnection();
            try {
                if (connectionHandler.initiateConnection()) {
                    productUrlsAndLastMod = getProductUrls(connection.getInputStream());
                }
            } catch (IOException e) {
                if (e instanceof MalformedByteSequenceException) {
                    logger.warn("MalformedByteException for " + url);
                } else
                    logger.error(url + " IOexception!!!", e);
                Thread.sleep(1000);
                if (!(e instanceof SSLException || e instanceof MalformedByteSequenceException))
                    call();
            } finally {
                connection.disconnect();
                connection = null;
            }

            return productUrlsAndLastMod;
        }

        private HashMap<String, ProductURL> getProductUrls(InputStream is) throws IOException, SAXException {
            ProductURLSaxParser sp = new ProductURLSaxParser(is);
            List<ProductURL> productURLs = sp.getProductURLList();
            HashMap<String, ProductURL> productUrlsAndLastModMap = new HashMap<>();
            for (ProductURL productURL : productURLs) {
                if (isInFilter(productURL))
                    productUrlsAndLastModMap.put(productURL.getUrl().toString(), productURL);
            }
            return productUrlsAndLastModMap;
        }

        private boolean isInFilter(ProductURL productURL) {
            for (String filter : filters) {
                if (productURL.getUrl().getPath().contains(filter))
                    return true;
            }
            return false;
        }
    }


}
