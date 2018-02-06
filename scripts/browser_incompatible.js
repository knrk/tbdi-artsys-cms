if( document.cookie.indexOf('bypass_browser_incompatibility=1') === -1 )
{
    var time = new Date(new Date().getTime() + 60 * 60 * 1000);
    document.cookie = "prev_url="+encodeURIComponent(window.location.href)+"; path=/; expires=" + time.toGMTString();
    window.location.href = '/browser_incompatible.html';
}