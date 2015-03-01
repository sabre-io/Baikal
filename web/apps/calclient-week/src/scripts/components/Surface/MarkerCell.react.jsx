/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet;

var MarkerCell = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {

        var halfhourheight = (this.props.hourheight / 2);
        var classes = cx({
            'tg-markercell': true,
            'markercell-business-on': this.props.isBusinessTime,
            'markercell-business-on-last': this.props.isLastBusinessTime,
            'markercell-business-off': !this.props.isBusinessTime
        });

        return (
            <div className={classes} style={{height: this.props.hourheight + 'px'}}><div className="tg-dualmarker" style={{
                height: halfhourheight + 'px',
                marginBottom: halfhourheight + 'px'
            }}></div></div>
        );
    }
});

module.exports = MarkerCell;