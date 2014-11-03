/**
 * @jsx React.DOM
 */

'use strict';

var React = require('react/addons');

var CommonIndentedIconLabel = React.createClass({
    render: function() {

        var containerclassnames = 'component-indentediconlabel clearfix ' + this.props.className;
        var iconclassnames = 'fa fa-' + this.props.icon;

        return (
            <div className={containerclassnames}>
                <div className="indentedlabel"><i className={iconclassnames}></i></div>
                <div className="indentedcontent">
                    {this.props.children}
                </div>
            </div>
        );
    }

})

module.exports = CommonIndentedIconLabel;