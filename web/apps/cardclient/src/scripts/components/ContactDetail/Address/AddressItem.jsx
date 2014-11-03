/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons'),
    TextLabel = require('../../Common/Indented/TextLabel'),
    findType = require('../../Common/Lib/FindType');

var normalizeType = function(types) {
    if(!types || !types.length) { return 'address'; }

    var normalizedTypes = [];
    
    if(findType(types, ['work'])) { return 'work'; }
    if(findType(types, ['home'])) { return 'home'; }
    if(findType(types, ['main'])) { return 'main'; }
    return types[0];
};

var ContactDetailAddressListItem = React.createClass({
    render: function() {

        var address = this.props.address;
        var type = normalizeType(address.type);
        
        var addresscontent = (
            <div className='address'>
                <div className='building'>
                    {(address.extendedaddress.trim()!=='') && <div className="extendedaddress">{address.streetaddress}</div>}
                    {(address.streetaddress.trim()!=='') && <div className='streetaddress'>{address.streetaddress}</div>}
                </div>
                <div className='locality'>
                    {(address.postalcode.trim()!=='') && <span className='postalcode'>{address.postalcode} </span>}
                    {(address.postofficebox.trim()!=='') && <span className='postofficebox'>{address.postofficebox} </span>}
                    {(address.city.trim()!=='') && <span className='city'>{address.city}</span>}
                </div>
                <div className='region'>
                    {(address.region.trim()!=='') && <span className='state'>{address.region} </span>}
                    {(address.country.trim()!=='') && <span className='country'>{address.country.toUpperCase()}</span>}
                </div>
            </div>
        );

        return (
            <TextLabel label={type}>
                {addresscontent}
            </TextLabel>
        );
    }

})

module.exports = ContactDetailAddressListItem;