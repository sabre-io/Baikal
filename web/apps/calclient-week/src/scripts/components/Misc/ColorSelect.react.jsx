/** @jsx React.DOM */

'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet;

var ColorSelect = React.createClass({
    mixins: [PureRenderMixin],
    getInitialState: function() {
        return {
            open: false
        };
    },
    onItemClick: function(item) {
        if(this.props.itemvalue(this.props.selected) !== this.props.itemvalue(item)) {
            this.props.onChange(item);
        }
        this.setState({open: false});
    },
    render: function() {

        var self = this,
            selectedItem = null;

        var selectedValue = this.props.itemvalue(this.props.selected);

        var items = this.props.items.map(function(item) {
            
            var value = self.props.itemvalue(item),
                label = self.props.itemlabel(item),
                selected = false;

            if(value === selectedValue) {
                selectedItem = label;
                selected = true;
            }

            return (<li key={value} className={cx({
                'active': selected
            })}><a style={{cursor: 'pointer'}} onClick={self.onItemClick.bind(self, item)}>{label}</a></li>);
        });

        return (
            <div className={cx({
                dropdown: true,
                open: this.state.open
            })}>
                <a className={cx({
                    btn: true,
                    'btn-default': true,
                    active: this.state.open
                })} onClick={function() { self.setState({open: !self.state.open}); }}>
                    {selectedItem}
                    <i style={{marginLeft: '10px'}} className={cx({
                        fa: true,
                        'fa-caret-down': !this.state.open,
                        'fa-caret-up': this.state.open
                    })}></i>
                </a>
                <ul className="dropdown-menu">
                    {items}
                </ul>
            </div>
        );

        /*return (
            <div onClick={this.onClick} style={{
                'border': '1px solid rgba(0, 0, 0, 0.2)',
                'borderRadius': '5px',
                'padding': '5px',
                'display': 'inline-block'
            }}>{items}</div>
        );*/
    }
});

module.exports = ColorSelect;