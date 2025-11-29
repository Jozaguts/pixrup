type Status = 'pending' | 'active' | 'cancelled'

const VALID_STATUSES: Status[] = ['pending', 'active', 'cancelled'];
export function useStatus() {
    const isValidStatus = (status: string): status is Status => VALID_STATUSES.includes(status as Status);
        const normalizeStatus = (status: string): Status => isValidStatus(status) ? status as Status : 'pending'

    return {
        isValidStatus,
        normalizeStatus,
        VALID_STATUSES
    }
}