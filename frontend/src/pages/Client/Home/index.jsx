import Banner from "./Banner";
import MovieCommingSoon from "./MovieComingSoon";
import MovieShowing from "./MovieShowing";

const HomePage = () => {
    return (
        <>
            <Banner />
            <main className="flex flex-col items-center py-8 px-10" >
                <div className="container mx-auto px-4 py-8">
                    <div className="flex mb-6">
                        <MovieShowing />
                    </div>
                    <div className="flex mb-6">
                        <MovieCommingSoon />
                    </div>
                </div>
            </main>
            
        </>
    );
};

export default HomePage;