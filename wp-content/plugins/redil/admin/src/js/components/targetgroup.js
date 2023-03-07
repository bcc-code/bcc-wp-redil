import React from 'react'

const { __ } = wp.i18n;
const { PluginDocumentSettingPanel } = wp.editPost;
const { PanelRow, SelectControl }    = wp.components;
const { compose }         = wp.compose;
const { withSelect, withDispatch }   = wp.data;

const postTypes = [ 'post' , 'page' ]
const { groups } = window.redilData

const TargetGroup = ( { postType, postMeta, setPostMeta } ) => {

    if ( !postTypes.includes( postType ) ) return null;

    return (
        <PluginDocumentSettingPanel title={ __( 'Redil', 'redil') } initialOpen="true">
            <PanelRow>
                <SelectControl
                    label    = { __( 'Target Group', 'redil' ) }
                    value    = { postMeta.redil_group_id }
                    options  = { 
                        Object.keys(groups)
                            .map(key => { return { label: __(key['ID'], 'redil' ),  value: groups[key] } })
                    }
                    onChange = {
                        ( value ) => setPostMeta( { redil_group_id: value } )
                    }
                    __nextHasNoMarginBottom
                />
            </PanelRow>
        </PluginDocumentSettingPanel>
    )

}

export default compose( [
    withSelect( ( select ) => {
        return {
            postMeta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
            postType: select( 'core/editor' ).getCurrentPostType(),
        };
    }),
    withDispatch( ( dispatch ) => {
        return {
            setPostMeta ( newGroup ) {
                dispatch( 'core/editor' ).editPost( { meta: newGroup });
            }
        }
    })
] )( TargetGroup )