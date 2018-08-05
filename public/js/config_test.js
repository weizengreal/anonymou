/**
 * Created by zhengwei on 2017/5/21.
 */
$(function () {
    init();
    $(".menu").click(function () {
        $(".audit").css("display","none");
        $(".change").css("display","none");
        $(".comment").css("display","none");
        $(".configure").css("display","none");

        $("ul li a").attr("class","");
        $(this).attr('class','active');
        switch ($(this).data("type")) {
            case 1: {
                $(".audit").css("display","");
                break;
            }
            case 2: {
                $(".change").css("display","");
                break;
            }
            case 3: {
                $(".comment").css("display","");
                break;
            }
            case 4: {
                $(".configure").css("display","");
                break;
            }
        }
    });

    $(".app").on('click','.watchMore',function () {
        $(this).parent().prev().children();
        $("#teaname").html($(this).data('name'));
        $("#lessons").html($(this).data('lessons'));
        $("#detail").html($(this).data('detail'));
        $("#determine").data('tid',$(this).data('tid'));
        $("#determine").data('index',$(this).data('index'));
        var teaimg = $(this).data('teaimg');
        $('#teaimg img').attr('src',teaimg.length > 2 ? imgDomain + teaimg + '?imageView2/1/w/270/h/270' : '/anonymou/public/teaimg/'+teaimg+'.png');

        $("#moreInfo").modal('show');
    });

    $(".app").on('click','.pass',function () {
        if($(this).html() != '点我通过') {
            alert("这条信息已经通过审核!");
            return ;
        }
        var index = $(this).data('index');
        if(confirm("是否确认通过审核？")) {
            pass($(this).data('tid'),function (isOk) {
                if(isOk) {
                    deleteEle(index);
                    alert("审核通过成功");
                }
            });
        }
        else {
            return 1;
        }
    });

    $(".app").on('click','.drop',function () {
        if($(this).html() != '不予通过') {
            alert("这条信息已经弃置，若要显示于微评教上请审核通过!");
            return ;
        }
        var index = $(this).data('index');
        if(confirm("是否丢弃这条数据？")) {
            unPass($(this).data('tid'),function (isOk) {
                if(isOk) {
                    deleteEle(index);
                    alert("已将这条信息丢失到遥远的二次元空间！");
                }
            });
        }
        else {
            return 1;
        }
    });

    $("#determine").click(function () {
        var index = $(this).data('index');
        $("#moreInfo").modal('hide');
        pass($(this).data('tid'),function (isOk) {
            if(isOk) {
                deleteEle(index);
            }
        });
    });

    // 前一页
    $("#pre").click(function () {
        pageSign=1;
        getData(--page,handle);
    });

    // 下一页
    $("#next").click(function () {
        pageSign=-1;
        getData(++page,handle);
    });

    // 确定
    $("#configure").click(function () {
        $.post(changeTeaStu,{ check : $('.model:radio:checked').val() ,upImg : $('.upImg:radio:checked').val()} ,function (data) {
            if(data.status == 1) {
                alert("更改成功");
                window.location.reload();
            }
            else {
                alert(data.info);
            }
        })
    });

    // 搜索界面的分页
    $(".nowPage").click(function () {
        search($('#search').val(),$(this).html());
    });

    // 上传文件按钮
    $("#excelButton").click(function () {
        $("#upExcel").click();
    });

    // 上传文件
    $("#upExcel").change(function () {
        var fileDetail = $(this).val().split('\\');
        $('#uploadName').html('正在处理'+fileDetail[fileDetail.length-1]);
        $("#uploadExcelForm").ajaxSubmit(function (e) {
            if(e.status == 1) {
                $('#uploadName').html(fileDetail[fileDetail.length-1]+'自动导入成功');
            }
            else {
                $('#uploadName').html(fileDetail[fileDetail.length-1]+'自动导入出现错误，错误信息：'+e.info);
            }
        })
    });

    // 删除评论
    $(".app").on('click','.com_drop',function () {
        if(confirm('你确定要删除这条评论吗？')) {
            var commentId = $(this).data('commentid');
            deleteCom(commentId,function (isok) {
                if(isok) {
                    deleteComEle(commentId);
                    alert('成功删除该评论');
                }
            })
        }
    });

    // 评论搜索界面的分页
    $(".comNowPage").click(function () {
        searchCom($('#searchCom').val(),$(this).html());
    });
});

/*
 * 初始化函数
 * */
function init() {
    getData(page,handle);
    search('',1);
    searchCom('',comPage);
}

/*
 * 通过审核函数
 * 需要传入该教师的唯一标记=>id
 * */
function pass(id,fn_success) {
    $.post(setPass,{tid : id},function (data) {
        // console.log(data);
        if(data.status == 1) {
            fn_success(true);
        }
        else {
            alert(data.info);
        }
    })
}

/*
 * 不通过审核函数
 * 需要传入该教师的唯一标记=>id
 * */
function unPass(id,fn_success) {
    $.post(setUnPass,{tid : id},function (data) {
        // console.log(data);
        if(data.status == 1) {
            fn_success(true);
        }
        else {
            alert(data.info);
        }
    })
}

/*
 * 获取数据
 * */
function getData(page,fn_success) {
    if(page == 0) {
        fn_success([]);
    }
    $.ajax({
        url : getDataUrl,
        type : "POST",
        data:{page : page},
        dataType : "JSON",
        success : function (response) {
            fn_success(response.data);
        },
        error : function () {
            alert('网络错误，请刷新或者稍后再试！');
        }
    })
}

/*
 * 处理数据并渲染进入界面
 * */
function handle(data) {
    if(data.length == 0) {
        page == 1 ? "": (page+=pageSign) && alert("已经到尽头啦！");
        return ;
    }
    var teaItem = $("#teaItem").html(),teaBody=$('#teaBody');
    teaBody.html("");// 清空该部分并重新渲染
    for( var index in data) {
        var lessons = "";
        for(var j in data[index].lessons) {
            j==data[index].lessons.length-1 ? lessons+=data[index].lessons[j] : lessons+=data[index].lessons[j]+" | ";
        }
        teaBody.append(teaItem._format(index,data[index].name,lessons,data[index].detail,data[index].tid,data[index].teaimg));
    }
}

/*
 * 审核完成之后删除当前element元素
 * */
function deleteEle(index) {
    $(".teaItem[index = "+index+"]").remove();
}

/*
 * 评论部分：审核完成之后删除当前element元素
 * */
function deleteComEle(index) {
    $(".comItem[index = "+index+"]").remove();
}

/*
* 查询函数
* */
function search(searchWord,seaPage) {
    var teaItem = $("#searchTeaItem").html(),teaBody=$('#teaChange');
    teaBody.html("");// 清空该部分并重新渲染
    $('#teaChangeTable').css('display','');
    // $('.page').css('display','none');
    $.ajax({
        url : searchTea,
        type : "POST",
        data:{searchWords : searchWord,page : seaPage},
        dataType : "JSON",
        success : function (response) {
            if(response.status == 1) {
                var data = response.data;
                for( var index in data) {
                    var lessons = "";
                    for(var j in data[index].lessons) {
                        j==data[index].lessons.length-1 ? lessons+=data[index].lessons[j] : lessons+=data[index].lessons[j]+" | ";
                    }
                    teaBody.append(teaItem._format(index,data[index].name,lessons,data[index].detail,data[index].tid,data[index].show == 1 ? '已通过' : '点我通过',data[index].show == 3 ? '已弃置' : '不予通过',data[index].teaimg));
                    // console.log(data[index].show == 1);
                }
            }
            else {
                alert("网络错误，请刷新后重试！");
            }
        },
        error : function () {
            alert('网络错误，请刷新或者稍后再试！');
        }
    })
}


/*
* 查询评论
* */
function searchCom(searchWord,seaPage) {
    var teaItem = $("#commentItem").html(),teaBody=$('#comBody');
    teaBody.html("");// 清空该部分并重新渲染
    $.ajax({
        url : searchComUrl,
        type : "POST",
        data:{searchWords : searchWord,page : seaPage},
        dataType : "JSON",
        success : function (response) {
            if(response.status == 1) {
                console.log(response.data);
                var data = response.data;
                for( var index in data) {
                    var lessons = "";
                    for(var j in data[index].lessons) {
                        j==data[index].lessons.length-1 ? lessons+=data[index].lessons[j] : lessons+=data[index].lessons[j]+" | ";
                    }
                    teaBody.append(teaItem._format(data[index].name,lessons,data[index].content,data[index].cretime,data[index].cid));
                    // console.log(data[index].show == 1);
                }
            }
            else {
                alert("网络错误，请刷新后重试！");
            }
        },
        error : function () {
            alert('网络错误，请刷新或者稍后再试！');
        }
    })
}

/*
* 删除一条评论
* */
function deleteCom(comid,fn_success) {
    $.post(deleteComUrl,{commentId : comid},function (data) {
        // console.log(data);
        if(data.status == 1) {
            fn_success(true);
        }
        else {
            alert(data.info);
        }
    })
}