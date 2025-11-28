import { useQuery } from "@pinia/colada";
import route from "@/../js/routes/reportspdf";

interface ReturnData {
    logos: any[];
}

const useFetchLogos = () => {
    return useQuery<ReturnData>({
        key: ['fetch-avatars'],
        query: async () => fetchLogos(),
        enabled: true,
    });
};

const fetchLogos = async (): Promise<ReturnData> => {
    try {
        const response = await window.axios.get(route.logos.url());

        return response.data as ReturnData;
    } catch (err) {
        // Let pinia-colada catch the error
        throw err;
    }
};


export default useFetchLogos;
