function copyLink(articleUrl) {
    var tempInput = document.createElement("input");
    document.body.appendChild(tempInput);
    tempInput.value = articleUrl;
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    alert("Makale linki kopyalandı!");
}

function sharePost(title, articleUrl) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: articleUrl
        })
        .then(() => console.log('Paylaşım başarılı!'))
        .catch(error => console.error('Paylaşım sırasında bir hata oluştu:', error));
    } else {
        alert('Bu tarayıcı paylaşım özelliğini desteklemiyor.');
    }
}
