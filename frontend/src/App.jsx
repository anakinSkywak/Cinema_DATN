import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { HistoryRouter } from './components/HistoryRouter';
import routes from './routes';
import PrivateRoute from './components/PrivateRoute';
import NotFound from './components/NotFound';
import myHistory from './utils/myHistory';
import "./style.css";

function App() {
    return (
        <HistoryRouter history={myHistory}>
            <div className="App">
                <Routes>
                    {routes.map((page, index) => {
                        const loginRequire = page.private;
                        const roles = page.roles;
                        return (
                            <Route
                                key={index}
                                path={page.path}
                                element={
                                    page.layout ? (
                                        loginRequire ? (
                                            <PrivateRoute roles={roles}>
                                                <page.layout>
                                                    <page.component />
                                                </page.layout>
                                            </PrivateRoute>
                                        ) : (
                                            <page.layout>
                                                <page.component />
                                            </page.layout>
                                        )
                                    ) : (
                                        <page.component />
                                    )
                                }
                            />
                        );
                    })}
                    <Route path="*" element={<NotFound />} />
                </Routes>
            </div>
        </HistoryRouter>
    );
}

export default App;
