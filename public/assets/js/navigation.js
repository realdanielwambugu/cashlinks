$(document).on('click', 'a', function (e)
{
    var href = config().host + '/' + $(this).attr('href').replace(/^\/|\/$/g, '');

    history.pushState(null, null, href);

    $('body').html(loader());

    request(href);

    e.preventDefault();

});

$(window).on('popstate', function ()
{
    $('body').html(loader());

    request(location.pathname);
});

function request(href)
{
    $.get(href, function (data)
    {
       data = data.replace(/^.*?<body[^>]*>(.*?)<\/body>.*?$/s,"$1");

        $("body").html(data);
    });
}

function loader()
{
   return`<div class='w-full h-screen ff-pri fx fx-j-c fx-i-c fs-xl fw-black
          color-pri'>
            <i class='far fa-spinner pr-2'> </i>
            <span class ='txt-h-c'> Loading </span>
        </div>`;
}
