(function () {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls, PanelColorSettings, FontSizePicker, ColorPalette } = wp.blockEditor;
    const { __ } = wp.i18n;
    const { PanelBody, RangeControl, TextControl, SelectControl, Icon, TabPanel, __experimentalBoxControl } = wp.components;

    registerBlockType('wpdirectorykit/wdk-block-share', {
        title: 'Share Button',
        icon: 'share-alt',
        category: 'wdk-blocks',
        attributes: {
            linkText: {
                type: 'string',
                default: 'Share this page',
            },
            specialClass: {
                type: 'string',
                default: 'default-class',
            },
            padding: {
                type: 'object',
                default: { top: '0px', right: '0px', bottom: '0px', left: '0px' },
            },
            margin: {
                type: 'object',
                default: { top: '0px', right: '0px', bottom: '0px', left: '0px' },
            },
            borderWidth: {
                type: 'number',
                default: '0',
            },
            fontSize: {
                type: 'number',
                default: 16,
            },
            textColor: {
                type: 'string',
                default: '#000000',
            },
            backgroundColor: {
                type: 'string',
                default: '#ffffff',
            },
        },
        example: {
            attributes: {
                linkText: 'Share this зфпу',
                specialClass: 'example-class',
                textColor: '#ffffff',
                backgroundColor: '#0073aa',
            },
        },
        edit: function (props) {
            const {
                attributes: {
                    linkText, specialClass, padding, margin, fontSize, textColor, backgroundColor,
                    borderWidth, borderStyle, borderColor
                },
                setAttributes,
            } = props;

            return el(
                Fragment, {},

                el(
                    InspectorControls, {},
                    el(
                        TabPanel, {
                            tabs: [
                                {
                                    name: 'general',
                                    title: el(Fragment, {}, el(Icon, { icon: 'admin-generic' }), ' General'),
                                    className: 'general-tab',
                                },
                                {
                                    name: 'style',
                                    title: el(Fragment, {}, el(Icon, { icon: 'art' }), ' Style'),
                                    className: 'style-tab',
                                },
                            ],
                        },
                        (tab) => {
                            if (tab.name === 'general') {
                                return el(
                                    Fragment, {},
                                    el(
                                        PanelBody, { title: 'Block Settings', initialOpen: true },
                                        el(TextControl, {
                                            label: 'Button Text',
                                            value: linkText,
                                            onChange: (value) => setAttributes({ linkText: value })
                                        }),
                                        el(TextControl, {
                                            label: 'Special Class',
                                            value: specialClass,
                                            onChange: (value) => setAttributes({ specialClass: value })
                                        }),
                                    )
                                );
                            }

                            if (tab.name === 'style') {
                                return el(
                                    Fragment, {},
                                    el(
                                        PanelBody, { title: 'Border Settings', initialOpen: true },
                                        el(RangeControl, {
                                            label: 'Border Width',
                                            values: borderWidth,
                                            onChange: (newWidth) => setAttributes({ borderWidth: newWidth }),
                                        }),
                                        el(SelectControl, {
                                            label: 'Border Style',
                                            value: borderStyle,
                                            options: [
                                                { label: 'None', value: 'none' },
                                                { label: 'Solid', value: 'solid' },
                                                { label: 'Dashed', value: 'dashed' },
                                                { label: 'Dotted', value: 'dotted' },
                                            ],
                                            onChange: (newStyle) => setAttributes({ borderStyle: newStyle }),
                                        }),
                                        el(ColorPalette, {
                                            label: 'Border Color',
                                            value: borderColor,
                                            onChange: (newColor) => setAttributes({ borderColor: newColor }),
                                        })
                                    ),
                                    el(
                                        PanelBody, { title: 'Space Control', initialOpen: true },
                                        el(__experimentalBoxControl, {
                                            label: 'Padding',
                                            values: padding,
                                            onChange: (newPadding) => setAttributes({ padding: newPadding }),
                                        }),
                                        el(__experimentalBoxControl, {
                                            label: 'Margin',
                                            values: margin,
                                            onChange: (newMargin) => setAttributes({ margin: newMargin }),
                                        }),
                                    ),
                                    el(
                                        PanelBody, { title: 'Font', initialOpen: true },
                                        el(FontSizePicker, {
                                            fontSizes: [
                                                { name: 'small', size: 12 },
                                                { name: 'medium', size: 16 },
                                                { name: 'large', size: 20 },
                                                { name: 'extra large', size: 24 }
                                            ],
                                            value: fontSize,
                                            onChange: (value) => setAttributes({ fontSize: value })
                                        }),
                                    ),
                                    el(PanelColorSettings, {
                                        title: 'Color Settings',
                                        colorSettings: [
                                            {
                                                value: textColor,
                                                onChange: (color) => setAttributes({ textColor: color }),
                                                label: 'Text Color',
                                            },
                                            {
                                                value: backgroundColor,
                                                onChange: (color) => setAttributes({ backgroundColor: color }),
                                                label: 'Background Color',
                                            }
                                        ]
                                    })
                                );
                            }
                        }
                    )
                ),
                el(
                    'a',
                    { 
                        className: `wdk-block-share ${specialClass}`, 
                        style: { 
                                padding: `${padding.top} ${padding.right} ${padding.bottom} ${padding.left}`, 
                                margin: `${margin.top} ${margin.right} ${margin.bottom} ${margin.left}`, 
                                fontSize: `${fontSize}`, 
                                color: textColor, 
                                backgroundColor,
                                borderWidth,
                                borderStyle,
                                borderColor,
                                textDecoration: 'none',
                                display: 'inline-block' 
                        },
                        href: '',
                    },
                    linkText
                )
            );
        },
        save: function (props) {
            const {
                attributes: {
                    linkText, specialClass, padding, margin, fontSize, textColor, backgroundColor,
                    borderWidth, borderStyle, borderColor
                },
            } = props;

            return el(
                'a',
                { 
                    className: `wdk-block-share ${specialClass}`, 
                    style: { 
                            padding: `${padding.top} ${padding.right} ${padding.bottom} ${padding.left}`, 
                            margin: `${margin.top} ${margin.right} ${margin.bottom} ${margin.left}`, 
                            fontSize: `${fontSize}`, 
                            color: textColor, 
                            backgroundColor,
                            borderWidth,
                            borderStyle,
                            borderColor,
                            textDecoration: 'none',
                            display: 'inline-block' 
                        },
                    href: '#',
                },
                linkText
            );
        }
    });
})();
