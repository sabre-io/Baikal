/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    TextLabel = require('../../Common/Indented/TextLabel'),
    findType = require('../../Common/Lib/FindType');

var normalizeType = function(types) {
    
    if(!types || !types.length) { return 'website'; }

    var normalizedTypes = [];
    
    if(findType(types, ['work'])) { return 'work'; }
    if(findType(types, ['home'])) { return 'home'; }
    if(findType(types, ['main'])) { return 'main'; }
    return types[0];
};

var ContactDetailURLListItem = React.createClass({
    render: function() {

        var url = this.props.url;
        var type = normalizeType(url.type);

        var urldest = url.value;
        if(!urldest.match(/^[a-z]+?:\/\//i)) {
            urldest = 'http://' + urldest;
        }

        return (
            <TextLabel label={type}>
                <a href={urldest} target="_blank">{urldest}</a>
                <span> </span>
                {url.prefered && <i className="fa fa-star" style={{color: 'gold'}} />}
            </TextLabel>
        );
    }

})

module.exports = ContactDetailURLListItem;