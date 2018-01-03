<!DOCTYPE html>
<html>
<head>
    <title>Изменение поста</title>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap/bootstrap.bundle.min.js"></script>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '.postText',
            height: '300px',
            plugins: [
                 "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                 "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                 "table contextmenu directionality emoticons paste textcolor responsivefilemanager code fullscreen"
           ],
           toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
           toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
           image_advtab: true ,
           
           external_filemanager_path:"/filemanager/",
           filemanager_title:"Responsive Filemanager" ,
           external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
        });
    </script>
    <style type="text/css">
        body{
            padding-bottom: 250px;
        }
        form{
            float: none !important;
            margin: 30px auto 0;
            padding: 0 !important;
        }
    </style>
</head>
<body>
    <?php $a = $args['article']; ?>
    <form id="formNewPost" class='col-md-8' method="POST" action="">
        <h3>Заголовок:</h3>
        <input type="text" class="form-control" name='header' value="<?php echo $a->header?>"><br>
        <h3>Основной текст поста:</h3>
        <textarea class="postText" name='postText'><?php echo $a->description?></textarea><br>
        <h3>Превью:</h3>
        <textarea class="postText" name='previewText'><?php echo $a->short_description?></textarea><br>
        <h3>Выбери категорию:</h3>
        <select id='categories' class='form-control' name='category'>
            <option value=""></option>
            <?php 
                foreach ($args['categories'] as $cat) {
                    $selected = '';
                    if(isset($a->category) && $cat->id == $a->category) $selected = 'selected';
                    echo "<option value='$cat->id' $selected>$cat->category</option>";
                }
            ?>
        </select><br>
        <?php if ($a->status != 1): // не опубликован ?>
            <a class="submit btn btn-default btn-lg" href="/prevPost/<?php echo $a->id; ?>">Предпросмотр</a>&nbsp;
            <a class="submit btn btn-default btn-lg" href="/admin/publicPost/<?php echo $a->id; ?>">Опубликовать</a>&nbsp;
            <a class="submit btn btn-default btn-lg" href="/admin/updatePost/<?php echo $a->id; ?>">Сохранить</a>  
        <?php else:?>
            <a class="submit btn btn-default btn-lg" href="/admin/updatePost/<?php echo $a->id; ?>">Обновить пост</a>
        <?php endif; ?>
        <a class="btn btn-danger btn-lg" href="/admin">Отмена</a>
    </form>
    <script type="text/javascript">
        $('.submit').on('click', function(e){
            e.preventDefault();

            var action = $(this).attr('href');
            $('#formNewPost').attr('action', action).submit();

            return false;
        })
    </script>
</body>
</html>