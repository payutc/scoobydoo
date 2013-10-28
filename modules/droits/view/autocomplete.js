var map = {};
$('.typeahead-user').typeahead({
    source: function (query, process) {
        return $.get('<?php echo $this->param['autocomplete']; ?>', { query: query }, function (data) {
            list_name = []
            for(item in data.result) {
                map[data.result[item].name] = data.result[item].id;
                list_name.push(data.result[item].name);
            }
            return process(list_name);
        });
    },
    updater: function(item) {
        $('#usr_id').val(map[item]);
        return item;
    }
});
