function spoiler(id)
{
    var element = document.getElementById(id);
    var icon = document.getElementById('l' + id);

    if (element.style['display'] === 'none' || element.style['display'] === '') {
        icon.className = 'fa fa-angle-down';
        element.style['visibility'] = 'visible';
        element.style['display'] = 'block';
    } else {
        icon.className = 'fa fa-angle-right';
        element.style['visibility'] = 'hidden';
        element.style['display'] = 'none';
    }

    chat.data.scrollpaneAPI.resize();
}