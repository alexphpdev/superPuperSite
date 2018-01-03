<!-- Modal -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Добавить категорию</h4>
      </div>
      <div class="modal-body">
        <form id="newCategoryForm" method="post" action="/admin/createCategory">
          <div class="form-group">
            <label for="categoryName" class="control-label">Название категории:</label>
            <input type="text" class="form-control" id="categoryName" name="categoryName" autofocus>
          </div>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-primary newCategory" id="submitNewCategoryForm">Создать</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(function(){
    $('#submitNewCategoryForm').on('click', function(){
      $('#newCategoryForm').submit();
    })
  })
</script>