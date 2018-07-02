$(document).ready(function () {
    cleaningItemOptionInit();
});

function cleaningItemOptionInit() {

    var $collectionHolder;

    var $addCleaningItemOptionLink = $('#btn_add_cleaningItemOption');
    var $newLinkPanel = $('#panel_add_cleaningItemOption');

    $collectionHolder = $('div#cleaningItemOption');

    $collectionHolder.find('div.m-portlet__body').each(function () {
        addCleaningItemOptionFormDeleteLink($(this));
    });

    var index = $collectionHolder.find('div.m-portlet').length;

    $collectionHolder.data('index', index);

    /*if (index == 0) {
        addCleaningItemOptionForm($collectionHolder, $newLinkPanel);
    }*/

    $addCleaningItemOptionLink.on('click', function (e) {
        e.preventDefault();
        addCleaningItemOptionForm($collectionHolder, $newLinkPanel);
    });
}

function addCleaningItemOptionForm($collectionHolder, $newLinkPanel) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    var new_index = index + 1;

    $collectionHolder.data('index', new_index);

    var $newFormPanelBody = $('<div class="m-portlet__body"></div>').append(newForm);
    var $newFormPanel = $('<div class="m-portlet m-portlet--rounded"></div>').append($newFormPanelBody);
    $newLinkPanel.before($newFormPanel);

    $('.make-switch').bootstrapSwitch();

    addCleaningItemOptionFormDeleteLink($newFormPanelBody, $newFormPanel);

    return new_index;
}

function addCleaningItemOptionFormDeleteLink($newFormPanelBody, $newFormPanel) {
    var $removeForm = $('<div class="row"><div class="col-md-3"><div class="form-group"><label class="form-control-label">Actions</label><br><div class="btn btn-sm btn-danger m-btn m-btn--icon m-btn--air" style="cursor: pointer"><span><i class="la la-trash-o"></i><span>Delete</span></span></div></div></div></div>');
    $newFormPanelBody.append($removeForm);

    if ($newFormPanel == null) {
        $newFormPanel = $newFormPanelBody.parent('.m-portlet');
    }

    var $collectionHolder = $('div#cleaningItemOption');

    $removeForm.on('click', function (e) {
        e.preventDefault();
        $newFormPanel.remove();

        var index = $collectionHolder.find('div.m-portlet').length;

        $collectionHolder.removeData('index');
        $collectionHolder.data('index', index);
    });
}
