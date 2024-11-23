import { Button } from 'antd';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSearch, faEdit, faTrash } from '@fortawesome/free-solid-svg-icons';

function UserActions({ record, setIsDetailOpen, setIsEditOpen, setIsDeleteOpen }) {
    return (
        <div className="flex gap-3">
            <Button
                className="text-blue-500 border border-blue-500"
                onClick={() => setIsDetailOpen({
                    id: record.id,
                    isOpen: true
                })}
            >
                <FontAwesomeIcon icon={faSearch} />
            </Button>
            <Button
                className="text-green-500 border border-green-500"
                onClick={() => setIsEditOpen({
                    id: record.id,
                    isOpen: true
                })}
            >
                <FontAwesomeIcon icon={faEdit} />
            </Button>
            <Button
                className="text-red-500 border border-red-500"
                onClick={() => setIsDeleteOpen({
                    id: record.id,
                    isOpen: true
                })}
            >
                <FontAwesomeIcon icon={faTrash} />
            </Button>
        </div>
    );
}

export default UserActions;