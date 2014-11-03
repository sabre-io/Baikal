/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons');

var CommonAvatar = React.createClass({
    render: function() {
        var photo = this.props.photo;

        if(!photo) {
            return (<div className="component-avatar">
                <div className="component-avatar-default">{this.props.caps}</div>
            </div>);
        }

        return (
            <div className="component-avatar">
                {photo && <img className="img-circle" src={'data:image/' + photo.type.toLowerCase() + ';base64,' + photo.value} />}
            </div>
        );
    }

})

module.exports = CommonAvatar;