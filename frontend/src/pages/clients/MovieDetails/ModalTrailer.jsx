import { Modal } from "antd";

const ModalTrailerPage = ({ closeModal, trailerUrl }) => {
    return (
        <>
            <Modal
                visible={true}
                onCancel={closeModal}
                footer={null} 
                width={800}
            >
                <div className="modal-trailer-content">
                    <iframe
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        className="w-full h-64"
                        src={trailerUrl}
                        title="YouTube video player"
                        style={{ width: '100%', height: '300px' }}
                    />
                </div>
            </Modal>
        </>
    );
}

export default ModalTrailerPage;