import './adminlayout.scss';
import Header from '../components/Header';
import Navbar from '../components/Navbar';

function AdminLayout({ children }) {
    return (
        <>
            <Header />
            <div className="main pt-[58px]">
                <div className="grid grid-cols-12">
                    <div className="col-span-2">
                        <Navbar />
                    </div>
                    <div className="w-full min-h-screen col-span-10  mx-auto">
                        <div className="p-[1.5rem] h-full">{children}</div>
                        <div className="lg:px-[36rem] pt-3 pb-2.5 flex-col bg-[#0D111AFF] text-center">
                            <div className="text-slate-700 ">
                                © Bản quyền
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

export default AdminLayout;
