import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api/students";

const studentService = {
    getStudents: async () => {
        const response = await axios.get(API_URL);
        return response.data;
    },

    getStudent: async (id) => {
        const response = await axios.get(`${API_URL}/${id}`);
        return response.data;
    },

    createStudent: async (studentData) => {
        const response = await axios.post(API_URL, studentData);
        return response.data;
    },

    updateStudent: async (id, studentData) => {
        const response = await axios.put(
            `${API_URL}/${id}`,
            studentData
        );

        return response.data;
    },

    deleteStudent: async (id) => {
        const response = await axios.delete(`${API_URL}/${id}`);
        return response.data;
    },
};

export default studentService;