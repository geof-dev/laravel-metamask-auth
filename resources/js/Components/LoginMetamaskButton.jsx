import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { useForm  } from '@inertiajs/react';
import { ethers } from "ethers";

export default function LoginMetamaskButton() {
    const form = useForm({
        eth_address: ''
    });

    const metamaskLogin = async () => {
        const provider = new ethers.BrowserProvider(window.ethereum);
        const signer = await provider.getSigner();
        signer.getAddress().then((value) => {
            form.setData({eth_address: value});
            form.post(route('metamask.login'));
        });
    }

    return (
        <div className="flex items-center flex-col mt-4">
            <PrimaryButton className="ml-4" onClick={metamaskLogin} >
                Log in with MetaMask
            </PrimaryButton>
            <InputError className="ml-4"message={form.errors.metamask} className="mt-2" />
        </div>
    );
}
