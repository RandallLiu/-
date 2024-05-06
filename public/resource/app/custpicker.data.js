
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define('CustData', [], factory);
    } else {
        factory();
    }
})(function () {

    var CustData = {};

    $.ajaxSetup({
        async:false,
        cache:true
    });

    comm.GET({
        url : '/archives/project/get_cust_project',
        success  :function (resp) {
            const arr = Object.keys(resp.data);
            if ( resp.status && arr.length) {
                CustData = resp.data;
            }
        }
    });

    if (typeof window !== 'undefined') {
        window.CustData = CustData;
    }

    return CustData;

});
