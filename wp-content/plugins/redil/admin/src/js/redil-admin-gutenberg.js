/**
 * WordPress dependencies.
 */
const { registerPlugin } = wp.plugins;

/**
 * Local dependencies.
 */
import TargetGroup from './components/targetgroup.js';

registerPlugin('redil', {
    render() {
        return (<TargetGroup />);
    }
})