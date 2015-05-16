M.report_filetrash = {};

M.report_filetrash.init = function(Y) {

    Y.on('change', function(e) {
        Y.all('input[type="checkbox"][name^="orphan_"]').each(function() {
            this.set('checked', e.target.get('checked'));
        });
    }, 'input[type="checkbox"][name="selectall"]');
};
