var ModelUpdateMixin = {

    componentWillMount: function(){
        this.props.model.on("change", (function() {
            this.forceUpdate();
        }.bind(this)));
    },

    componentWillUnmount: function(){
        this.props.model.off("change");
    }

};

module.exports = ModelUpdateMixin;