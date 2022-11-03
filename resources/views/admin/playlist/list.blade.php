@extends('admin.main')

@section('content')
    <button class="btn btn-primary btn-sm" type="button" id="button_del" href="#"
            onclick="delid()" style="visibility: hidden; height: 50px;width: 100px; z-index: 10000;position: fixed;bottom: 0;left: 0" >
        <i class="fas fa-trash"></i>
    </button>
    <table class="table" style="position: absolute;">
        <thead style="background: #0c84ff;color: white">
        <tr style="text-align: center">
            <th style="width: 100px; line-height: normal">
                <input type="checkbox" name="del_all" onclick="delall()" style="height: 20px;width: 20px; float: left" > Xóa
            </th>
            <th>STT</th>
            <th>Hình ảnh</th>
            <th>Tên</th>
            <th>
                <select style="text-align: center" class="form-control" name="menu_id" id="menu_id" >
                    <option style="text-align: center" value="*">Danh mục</option>
                    @foreach($menus as $menu)
                        <option style="text-align: center" value="{{$menu->id}}">{{$menu->name}}</option>
                    @endforeach
                </select>
            </th>
{{--            <th>Danh sách bài hát</th>--}}
            <th>Kích hoạt</th>
            <th style="width: 100px"  >Sửa</th>
            <th style="width: 100px"  >Danh sách</th>
        </tr>
        </thead>
        @php
            $counter=1;
        @endphp
        <tbody style="text-align: center" id="danhsachbaihat">
        @foreach($playlistxs as $playlist)
            <tr>
                <td>
                    <input type="checkbox" name="del_id[]" onclick="showbutton()" style="height: 20px;width: 20px"  value="{{$playlist->id}}" >
                </td>
                <td> {{ $counter }} </td>
                <td> <img src="{!! $playlist->thumb !!}" width="80px" height="80px"> </td>
                <td style="width: auto">{{$playlist->name}}</td>
                <td>
                    <label class="form-check-label">
                        {{ $playlist->menu_playlist->name }}
                    </label>
                    <br>
                </td>
{{--                <td>--}}
{{--                    <div class="form-control pl-4 " style="text-align:left; height: 100px;overflow-y: scroll;scrollbar-color: #656262;scrollbar-width: thin;" >--}}
{{--                        @foreach($playlist->playlist_song as $song)--}}
{{--                            <label class="form-check-label">--}}
{{--                                {{ $song->name }}--}}
{{--                            </label>--}}
{{--                            <br>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </td>--}}
                <td> <div id="parent_active_{{$playlist->id}}"> </div> {!!  \App\Http\Helper\Helper::active($playlist->active,$playlist->id,"/admin/playlist/change/".$playlist->id) !!}</td>
                <td>
                    <a class="btn btn-primary btn-sm" href="/admin/playlist/edit/{{$playlist->id}}" >
                        <i class="far fa-edit"></i>
                    </a>
                </td>
                <td>
                    <a class="btn btn-primary btn-sm" >
                        <i onclick="doitrangthai({{$playlist->id}})" class="far fa-solid fa-eye"></i>
                    </a>
                </td>
            </tr>
            @php
                $counter++;
            @endphp
        @endforeach
        </tbody>
    </table>


    <style>
        #listbaihat{overflow-y: scroll;scrollbar-color: #656262;scrollbar-width: thin;z-index: 1;  visibility: hidden;position: absolute;right: 35%;
            background: #0a0e14;
            height: 500px;
            opacity: 0.9;
            width: 500px;
            color: white;
            text-align: left;
        }

        #namesong{
            margin-left: 50px;
        }
    </style>


    <div id="listbaihat">

    </div>

    <script>
        function doitrangthai(id){
            if(document.getElementById("listbaihat").style.visibility == "hidden"){
                var url ='/admin/playlist/song/'+id;
                $.ajax({
                    type: 'GET',
                    datatype: 'JSON',
                    url: url,
                    success:function (result){
                        if (result.error == true){
                            var  html='';
                            $('#listbaihat').html(html);
                            alert('Hiện không có bài hát thể loại này');
                        }else {
                            console.log(result.song);
                            var  html='';
                            $.each(result.song, function (i,item){
                            html+='<label id="namesong">'+item.name+'</label><br>';
                            $('#listbaihat').html(html);
                        });
                        }
                    }});
                document.getElementById("listbaihat").style.visibility = "visible";
            }
            else
                document.getElementById("listbaihat").style.visibility = "hidden";
        };

        $(document).ready(function() {
            $('#menu_id').bind('change',
                function song_genre(){
                    var idmenu = document.getElementById('menu_id').value;
                    var url ='/admin/playlist/list';
                    if (idmenu!='*'){
                        url = url+'/'+idmenu;
                        $.ajax({
                            type: 'GET',
                            datatype: 'JSON',
                            url: url,
                            success:function (result){
                                if (result.error == true){
                                    var  html='';
                                    $('#danhsachbaihat').html(html);
                                    alert('Hiện không có bài hát thể loại này');
                                }else {
                                    console.log(result.playlist);
                                    var  html='';
                                    $.each(result.playlist, function (i,item){
                                        html +='<tr>';
                                        html +="<td> <input type='checkbox' name='del_id[]' onclick='showbutton()' style='height: 20px;width: 20px'  value='"+ item.id +"'> </td>";
                                        html +='<td>'+ (i+1) +'</td>';
                                        html +="<td> <img src='"+ item.thumb +"'  width='80px' height='80px'> </td>";
                                        html +='<td style="text-align: left;width: 200px;"><label class="form-check-label">'+ item.name +'</label></td>';
                                        html +='<td>'+ result.menu.name +'</td>';
                                        if (item.active==1)
                                            html +=" <td><div id='parent_active_"+item.id+"' ><span id='menu-yes-"+item.id+"' class='btn btn-success btn-xs' onclick=change_active("+ item.active +",'/admin/song/change/"+item.id+"')>Yes</span></div> </td>";
                                        else
                                            html +=" <td><div id='parent_active_"+item.id+"' > <span id='menu-no-"+item.id + "' class='btn btn-danger btn-xs' onclick=change_active("+ result.active +",'/admin/song/change/"+ item.id +"')>No</span></div>  </td>";
                                        html += "<td><a class='btn btn-primary btn-sm' href='/admin/playlist/edit/"+item.id+"' > <i class='far fa-edit'></i> </a></td>";
                                        html += "<td><a class='btn btn-primary btn-sm' > <i onclick='doitrangthai("+item.id +")' class='far fa-solid fa-eye'></i> </a></td>";
                                        html +='</tr>';
                                    });
                                    $('#danhsachbaihat').html(html);
                                }
                            }
                        })
                    }else {
                        location.reload();
                    }
                }
            );
        });
    </script>

    <script>
        function delall(){
            var $iddel = document.getElementsByName('del_id[]');
            var $delall = document.getElementsByName('del_all');
            if($delall[0].checked===true){
                document.getElementById('button_del').style.visibility='visible';
                for($i=0 ; $i<$iddel.length;$i++){
                    $iddel[$i].checked=true;
                }
            }
            else{
                document.getElementById('button_del').style.visibility='hidden';
                for($i=0 ; $i<$iddel.length;$i++){
                    $iddel[$i].checked=false;
                }
            }
        };

        function showbutton(){
            var $iddel = document.getElementsByName('del_id[]');

            for($i=0 ; $i<$iddel.length;$i++){
                if ($iddel[$i].checked===true){
                    return  document.getElementById('button_del').style.visibility='visible';
                }else {
                    document.getElementById('button_del').style.visibility='hidden';
                }
            }
        };

        function delid(){
            if(confirm('Dữ liệu xóa không thể khôi phục. Bạn có muốn xóa không?')){
                var $iddel = document.getElementsByName('del_id[]');
                for($i=0 ; $i <$iddel.length;$i++){
                    if($iddel[$i].checked===true){
                        removeRow($iddel[$i].value,'/admin/playlist/destroy');
                    }
                }
            }
            location.reload();
        };
    </script>
@endsection
