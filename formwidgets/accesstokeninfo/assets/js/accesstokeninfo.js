window.onload = function() {
  function getLoginLink(callback) {
    var httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = () => {
      if (httpRequest.readyState === XMLHttpRequest.DONE) {
        if (httpRequest.status === 200) {
          callback(httpRequest.responseText);
        } else {
          alert('There was a problem with the request.');
        }
      }
    }
    httpRequest.open('GET', '/get_login_url');
    httpRequest.send();
  }

  $('form').on('ajaxSuccess', function() {
    $('form').request('onSave', {
      data: {redirect: 0},
      success: function() {
        getLoginLink(function(loginLink) {
          window.location.href = loginLink;
        });
      }
    });
  })


};
