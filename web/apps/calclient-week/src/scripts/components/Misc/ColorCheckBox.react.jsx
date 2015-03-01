'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin;

var ColorCheckBox = React.createClass({
    mixins: [PureRenderMixin],
    getInitialState: function() {

        // The calendar will initially display the month of the current displayed week

        var state = {
            checked: this.props.checked || false,
            size: this.props.size || 16,
            color: this.props.color || 'rgb(40, 133, 255)'
        };

        state.uncheckedColor = this.props.uncheckedColor || state.color;

        return state;
    },
    componentWillReceiveProps: function(nextProps) {
        if(this.state.checked !== nextProps.checked) {
            this.setState({checked: nextProps.checked});
        }
    },

    onClick: function() {
        var checked = !this.state.checked;
        this.props.onChange(checked);
        this.setState({checked: checked});
    },
    render: function() {

        var inner = null;
        if(this.state.checked) {
            inner = (<i className="fa fa-check"></i>);
            //inner = (<span>✔</span>);
        }

        var bgColor = this.state.checked ? this.state.color : this.state.uncheckedColor;

        return (
            <div onClick={this.onClick} onDoubleClick={function(e) {e.stopPropagation(); e.preventDefault(); }} style={{
                'borderRadius': '3px',
                'textShadow': '0px 0px 3px rgba(0, 0, 0, 0.5)',
                'fontSize': (this.state.size - 4) + 'px',
                'lineHeight': this.state.size + 'px',
                'cursor': 'pointer',
                'backgroundColor': bgColor,
                'display': 'inline-block',
                'width': this.state.size + 'px',
                'height': this.state.size + 'px',
                'color': 'white',
                'verticalAlign': 'middle',
                'textAlign': 'center'
            }}>{inner}</div>
        );
    }
});

module.exports = ColorCheckBox;