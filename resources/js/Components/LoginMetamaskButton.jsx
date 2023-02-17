import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { router } from '@inertiajs/react';
import { ethers } from "ethers";
import { useState} from "react";

export default function LoginMetamaskButton() {
    const [errorMessage, setErrorMessage] = useState('');

    const metamaskLogin = async () => {
        let response = await fetch(route('metamask.signature'));
        const message = await response.text();
        const provider = new ethers.BrowserProvider(window.ethereum);
        const signer = await provider.getSigner();
        const address = await signer.getAddress();
        signer.signMessage(message).then((value) => {
            router.post(route('metamask.login'), {
                eth_address: address,
                signature: value,
            },{
                onError: (errors) => { setErrorMessage(errors.error) },
            })
        });
    }

    return (
        <div className="flex items-center flex-col mt-4">
            <PrimaryButton className="ml-4" onClick={metamaskLogin} >
                Log in with MetaMask
            </PrimaryButton>
            <InputError className="ml-4"message={errorMessage} className="mt-2" />
        </div>
    );
}
