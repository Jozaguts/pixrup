type Status = 'pending' | 'active' | 'cancelled'

const BADGED_CLASSES: Record<Status, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}
export function useBadgeClasses() {
    const getBadgeClasses = (status: Status): string =>  BADGED_CLASSES[status];

    return { getBadgeClasses };
}