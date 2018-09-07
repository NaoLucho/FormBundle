$(document).ready(function () {

    $file_arrayCollection = $("#file_array-field-list .form-group");
    $file_arrayCollection.each(function() {
        addDeleteBtn($(this));
    });


    jQuery('.add-another-file_array-widget').click(function (e) {
        e.preventDefault();
        var list = jQuery($(this).attr('data-list'));

        // Try to find the counter of the list
        var counter = list.data('widget-counter') | list.children().length;
        // If the counter does not exist, use the length of the list
        if (!counter) { counter = list.children().length; }

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data(' widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        addDeleteBtn(newElem);
        newElem.appendTo(list);
    });

    $('.file-wrapper input').on('change', function (e) {

        var label = $('.file-wrapper label[for="' + $(this).attr('id') + '"]');
        tab = this.value.split("\\");
        var html = tab[tab.length - 1];
        label.html(html);
    });
});

function addDeleteBtn($element) {
    var $removeFormA = $('<a href="#" type="button" class="btn btn-default" >Supprimer</a> <hr>');
    $element.append($removeFormA);

    $removeFormA.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $element.remove();
    });
}