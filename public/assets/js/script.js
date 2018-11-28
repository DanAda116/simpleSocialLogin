var activeTab = location.search.split('activeTab=')[1];

if (typeof activeTab !== 'undefined') {
    $('#'+activeTab).tab('show')
}

$('#user_avatar_avatarImage').on('change',function(){
    //get the file name
    var fileName = $(this).val().split('\\').pop();
    $('#file-info').html(fileName);
})






