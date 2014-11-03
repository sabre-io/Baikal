/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    TextLabel = require('../../Common/Indented/TextLabel'),
    findType = require('../../Common/Lib/FindType');

var normalizeType = function(types) {
    if(!types || !types.length) { return 'phone'; }

    var normalizedTypes = [];
    
    if(findType(types, ['fax'])) { return 'fax'; }
    if(findType(types, ['iphone'])) { return 'iPhone'; }
    if(findType(types, ['work'])) { return 'work'; }
    if(findType(types, ['home'])) { return 'home'; }
    if(findType(types, ['main'])) { return 'main'; }
    if(findType(types, ['cell', 'voice', 'mobile'])) { return 'mobile'; }
    return types[0];
};

var ContactDetailPhoneItem = React.createClass({
    render: function() {

        var phone = this.props.phone;
        var type = normalizeType(phone.type);

        return (
            <TextLabel label={type}>
                <div>{phone.value} <span> </span> {phone.prefered && <i className="fa fa-star" style={{color: 'gold'}} />}</div>
            </TextLabel>
        );
    }

});

module.exports = ContactDetailPhoneItem;