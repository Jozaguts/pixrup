import {useQuery} from "@pinia/colada";
import route from "@/../js/routes/reportspdf";

export const useFetchLogos = () => {
    return useQuery({
        key: ['fetch-avatars'],
        query:  ()=> fetchLogos(),
        enabled: true,
    });
}


const fetchLogos = async (): Promise<string[]> => {
    try {
        const response = await window.axios.get(route.logos.url());
        return response.data;
    } catch (err) {
        // Let pinia-colada catch the error
        throw err;
    }
};
