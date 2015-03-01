'use strict';

var React = require('react/addons'),
    PureRenderMixin = React.addons.PureRenderMixin,
    cx = React.addons.classSet;

var ArrowPopup = React.createClass({
    mixins: [PureRenderMixin],
    render: function() {

        var classSwitch = {
            'arrow-popup': true,
            'arrow-popup-anchor-left': this.props.anchorposition === 'left',
            'arrow-popup-anchor-right': this.props.anchorposition === 'right',
            'arrow-popup-anchor-offseted': this.props.anchoroffsettop > 0,
            'arrow-popup-anchor-not-offseted': this.props.anchoroffsettop === 0
        };

        if(this.props.className) {
            for(var additionalClassIndex in this.props.className) {
                classSwitch[this.props.className[additionalClassIndex]] = true;
            }
        }

        var classes = cx(classSwitch);

        return (
            (<div style={{
                top: this.props.top + 'px',
                left: this.props.left + 'px',
                width: this.props.width + 'px',
                height: this.props.height + 'px'
            }} className={classes}>
                <div className='arrow-popup-anchor' style={{top: this.props.anchoroffsettop + 'px'}}>
                    <span className="arrow-popup-anchor-outer"></span>
                    <span className="arrow-popup-anchor-inner"></span>
                </div>
                <div className="arrow-popup-content">{this.props.childs}</div>
            </div>)
        );
    }
});

module.exports = ArrowPopup;