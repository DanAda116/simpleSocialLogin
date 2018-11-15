$('#user_avatar_avatarImage').on('change',function(){
    //get the file name
    var fileName = $(this).val().split('\\').pop();
    $('#file-info').html(fileName);
})