/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    TextLabel = require('../../Common/Indented/TextLabel'),
    findType = require('../../Common/Lib/FindType');

var normalizeType = function(types) {

    if(!types || !types.length) { return 'email'; }

    var normalizedTypes = [];
    
    if(findType(types, ['work'])) { return 'work'; }
    if(findType(types, ['home'])) { return 'home'; }
    if(findType(types, ['main'])) { return 'main'; }
    if(findType(types, ['internet'])) { return 'email'; }

    return types[0];
};

var ContactDetailEmailListItem = React.createClass({
    render: function() {

        var email = this.props.email;
        var type = normalizeType(email.type);

        return (
            <TextLabel label={type}>
                <div>{email.value} <span> </span> {email.prefered && <i className="fa fa-star" style={{color: 'gold'}} />}</div>
            </TextLabel>
        );
    }

});

module.exports = ContactDetailEmailListItem;