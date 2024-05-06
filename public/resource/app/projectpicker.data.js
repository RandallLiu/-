
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define('ProjectData', [], factory);
    } else {
        factory();
    }
})(function () {

    var ProjectData = {};

    $.ajaxSetup({
        async:false,
        cache:true
    });

    comm.GET({
        url : '/archives/project/get_customer_project',
        success  :function (resp) {
            const arr = Object.keys(resp.data);
            if ( resp.status && arr.length) {
                ProjectData = resp.data;
            }
        }
    });

    if (typeof window !== 'undefined') {
        window.ProjectData = ProjectData;
    }
    return ProjectData;
});
