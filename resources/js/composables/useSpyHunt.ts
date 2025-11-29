import type { PropertyWorkspaceProperty } from '@/components/properties/workspace/types';

export default function useSpyHunt() {
    const formatAddress = (property: PropertyWorkspaceProperty) => {
        const primary = property.address?.line1 ?? property.title ?? null;
        const locality = [property.address?.city, property.address?.state]
            .filter(Boolean)
            .join(', ');
        const sections = [primary, locality].filter(
            (section): section is string => Boolean(section),
        );
        if (sections.length) {
            return sections.join(' • ');
        }
        return property.id ? `Property #${property.id}` : 'PixrSpyHunt';
    };


    return {
        formatAddress
    }
}