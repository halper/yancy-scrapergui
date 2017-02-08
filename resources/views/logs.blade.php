@extends('layouts.app')

@section('content')
    <div class="col-md-12">


        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Striped Full Width Table</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>File</th>
                        <th>Size</th>
                    </tr>
                    @for($i = 0; $i < sizeof($log_files); $i++)
                        <?php
                        $log_file = $log_files[$i];
                        ?>
                        <tr>
                            <td>{{$i+1}}</td>
                            <td><a href="/logs/{{$log_file['name']}}">{{$log_file['name']}}</a></td>
                            <td>
                                {{$log_file['size']}} kb
                            </td>
                        </tr>
                    @endfor

                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
@endsection