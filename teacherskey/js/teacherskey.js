function change_readonly(data) {
    if (!data.checked){
        document.getElementById('id_fio').removeAttribute('readonly')
    }else{
        document.getElementById('id_fio').setAttribute('readonly', true)
    }
}