(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useEffect, useState } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, PanelRow, TextControl, SelectControl } = wp.components;

    registerBlockType('my-plugin/wdk-latest-listing-block', {
        title: 'Latest Listings',
        icon: 'heart',
        category: 'wdk-blocks',
        attributes: {
            content: {
                type: 'string',
                source: 'html',
                selector: 'p',
            },
            alignment: {
                type: 'string',
                default: 'none',
            },
            postCount: {
                type: 'number',
                default: 5,
            },
        },
        edit: function(props) {
            const { attributes: { content, alignment, postCount }, setAttributes } = props;
            const [postHTML, setPostHTML] = useState('');

            useEffect(() => {
                wp.apiFetch({ path: `/wdk-blocks/v1/last-listings/&postCount=${postCount}` }).then(posts => {
                    setPostHTML(posts.data);
                });
            }, [postCount]);


            function onChangeContent(value) {
                setAttributes({ content: value });
            }

            function onChangeAlignment(value) {
                setAttributes({ alignment: value });
            }

            function onChangePostCount(value) {
                setAttributes({ postCount: parseInt(value, 10) });
            }

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Block Settings', initialOpen: true },
                        el(PanelRow, {},
                            el(TextControl, {
                                label: 'Content',
                                value: content,
                                onChange: onChangeContent,
                            })
                        ),
                        el(PanelRow, {},
                            el(SelectControl, {
                                label: 'Text Alignment',
                                value: alignment,
                                options: [
                                    { label: 'None', value: 'none' },
                                    { label: 'Left', value: 'left' },
                                    { label: 'Center', value: 'center' },
                                    { label: 'Right', value: 'right' },
                                ],
                                onChange: onChangeAlignment,
                            })
                        ),
                        el(PanelRow, {},
                            el(TextControl, {
                                label: 'Number of Posts',
                                type: 'number',
                                value: postCount,
                                onChange: onChangePostCount,
                            })
                        )
                    )
                ),
                el('div', { className: 'wdk-latest-listing-block' },
                    el('p', { style: { textAlign: alignment } }, content),
                    el('div', { dangerouslySetInnerHTML: { __html: postHTML } })
                )
            );
        },
        save: function(props) {
            const { attributes: { content, alignment, postCount } } = props;
            return el('div', { 
                className: 'wdk-latest-listing-block',
                'data-alignment': alignment,
                'data-post-count': postCount,
            },
                el('p', { style: { textAlign: alignment } }, content),
            );
        }
    });
})();
