import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { 
    PanelBody, 
    ToggleControl, 
    RangeControl, 
    TextControl 
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { building } from '@wordpress/icons';

registerBlockType('p116/directory', {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { showFlags, perPage, placeholderText } = attributes;
        
        const blockProps = useBlockProps({
            className: 'p116-directory-editor-placeholder'
        });

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Directory Settings', 'post116-business-directory')}>
                        <ToggleControl
                            label={__('Show Ownership Flags', 'post116-business-directory')}
                            checked={showFlags}
                            onChange={(value) => setAttributes({ showFlags: value })}
                            help={__('Display veteran, SAL, and auxiliary owned filters', 'post116-business-directory')}
                        />
                        
                        <RangeControl
                            label={__('Businesses Per Page', 'post116-business-directory')}
                            value={perPage}
                            onChange={(value) => setAttributes({ perPage: value })}
                            min={5}
                            max={100}
                            step={5}
                        />
                        
                        <TextControl
                            label={__('Search Placeholder Text', 'post116-business-directory')}
                            value={placeholderText}
                            onChange={(value) => setAttributes({ placeholderText: value })}
                            help={__('Text shown in the search input field', 'post116-business-directory')}
                        />
                    </PanelBody>
                </InspectorControls>
                
                <div {...blockProps}>
                    <div className="p116-editor-block">
                        <div className="p116-editor-icon">
                            {building}
                        </div>
                        <h3>{__('Business Directory', 'post116-business-directory')}</h3>
                        <p>{__('This block will display the business directory with search and filtering capabilities.', 'post116-business-directory')}</p>
                        <div className="p116-editor-settings">
                            <small>
                                {__('Settings:', 'post116-business-directory')} 
                                {perPage} {__('per page', 'post116-business-directory')}
                                {showFlags && ', ' + __('with ownership flags', 'post116-business-directory')}
                            </small>
                        </div>
                    </div>
                </div>
            </>
        );
    },

    save: () => {
        return null; // Server-side rendered block
    }
});