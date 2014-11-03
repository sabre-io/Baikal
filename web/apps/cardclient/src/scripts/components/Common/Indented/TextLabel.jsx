/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons');

var CommonIndentedTextLabel = React.createClass({
    render: function() {

        var containerclassnames = 'component-indentedtextlabel clearfix ' + this.props.className;
        var label = this.props.label;

        return (
            <div className={containerclassnames}>
                <div className="indentedlabel">{label}</div>
                <div className="indentedcontent">
                    {this.props.children}
                </div>
            </div>
        );
    }

})

module.exports = CommonIndentedTextLabel;