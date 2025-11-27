import { useMutation} from "@pinia/colada";
import {Ref} from "vue";
import reports from '@/routes/reportspdf/logos';

type StoreLogoParams = {
    file: Ref;
    onSucess: CallableFunction;
}
export const useStoreLogoMutation = ({ file, onSucess}:StoreLogoParams) => {
    return useMutation({
        key: ["store-logo"],
        mutation: () => storeLogo(file),
        onSuccess: () => onSucess()
    });
}


const storeLogo = (logo: Ref) => {
    if(!logo.value) {
        return Promise.reject("Please provide a logo file.");
    }
    const form = new FormData();
    form.append("logo", logo.value);
    form.append("name", logo.value.name);

    return window.axios.post(reports.new.url(), form, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};
