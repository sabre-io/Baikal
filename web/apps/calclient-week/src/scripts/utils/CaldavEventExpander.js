module.exports = {
    expand: function(ev, datefrom, dateto) {

        var expanded = {
            id: ev.id,
            title: ev.title,
            busy: ev.busy,
            occurences: []
        };

        expanded.occurences.push({
            'start': ev.start,
            'end': ev.end
        });

        return expanded;
    }
};