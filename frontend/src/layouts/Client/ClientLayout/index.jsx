import Header from '../components/Header';
import Footer from '../components/Footer';

function ClientLayout({ children }) {
    return (
        <>

            <Header />
            <div>{children}</div>
            <Footer />

        </>
    );
}

export default ClientLayout;