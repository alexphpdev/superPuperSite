<!-- Modal -->
<div class="modal fade" id="updateCategoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Изменить категорию</h4>
      </div>
      <div class="modal-body">
        <form id="updateCategoryForm" method="post" action="/admin/updateCategory">
          <div class="form-group">
            <label for="categoryNameUpdate" class="control-label">Название категории:</label>
            <input type="text" class="form-control" id="categoryNameUpdate" name="categoryName">
            <input type="hidden" id='categoryId' name="categoryId">
          </div>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-primary updateCategory" id="submitUpdateCategoryForm">Изменить</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(function(){
    $('.editCategory').on('click', function(){
      var categoryId = $(this).data('category-id');
      $.ajax({
        url: '/admin/getCategoryName',
        type: 'post',
        data: {'categoryId' : categoryId},
        success: function(res){
          $('#categoryNameUpdate').val(res);
          $('#categoryId').val(categoryId);
        }
      })
    });

    $('#submitUpdateCategoryForm').on('click', function(){
      $('#updateCategoryForm').submit();
    })
  })
</script>
