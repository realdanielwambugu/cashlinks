//clicking
function count_click(thread_id, link)
{
    (new Ajax).route('click')
              .data({thread_id:thread_id})
              .loader('click_load_' + thread_id)
              .send();

    var win = window.open(link, "_blank");
}
