const smartgrid = require('smart-grid');

smartgrid('./resources/less', {
    outputStyle: "less",
    columns: 12,
    offset: "5px",
    container: {
        maxWidth: "1650px",
        fields: "30px"
    },
    breakPoints: {
        lg: {
            width: "1280px",
            fields: "5px"
        },
        md: {
            width: "1024px",
            fields: "5px"
        },
        mdm: {
            width: "992px",
            fields: "5px"
        },
        sm: {
            width: "768px",
            fields: "5px"
        },
        xs: {
            width: "576px",
            fields: "5px"
        },
        xxs:{
            width: "362px",
            fields: "5px"
        }
    },
    oldSizeStyle: false
});