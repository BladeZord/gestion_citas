import axios from 'axios';
import React, { useEffect, useState } from 'react';
import { NumericFormat } from 'react-number-format';
import { Link } from 'react-router-dom';
import { Table, Button, Space, Typography, Popconfirm, message, TableColumnsType } from 'antd';

const { Title } = Typography;

interface Empleado {
  idEmpleado: number;
  nombre: string;
  departamento: string;
  sueldo: number;
}

export default function ListadoEmpleados(): React.ReactElement {
    const urlBase = "http://localhost:8080/rrhh-app/v1/es/empleados";

    const [empleados, setEmpleados] = useState<Empleado[]>([]);

    useEffect(() => {
        // cargarEmpleados();
    }, []);

    const cargarEmpleados = async (): Promise<void> => {
        try {
            const resultado = await axios.get<Empleado[]>(urlBase);
            console.log("Resultado de cargar empleados");
            console.log(resultado.data);
            setEmpleados(resultado.data);
        } catch (error) {
            message.error('Error al cargar los empleados');
        }
    };

    const eliminarEmpleado = async (id: number): Promise<void> => {
        try {
            await axios.delete(`${urlBase}/${id}`);
            message.success('Empleado eliminado correctamente');
            cargarEmpleados();
        } catch (error) {
            message.error('Error al eliminar el empleado');
        }
    };

    const columns: TableColumnsType<Empleado> = [
        {
            title: 'Id',
            dataIndex: 'idEmpleado',
            key: 'idEmpleado',
            align: 'center',
        },
        {
            title: 'Empleado',
            dataIndex: 'nombre',
            key: 'nombre',
            align: 'center',
        },
        {
            title: 'Departamento',
            dataIndex: 'departamento',
            key: 'departamento',
            align: 'center',
        },
        {
            title: 'Sueldo',
            key: 'sueldo',
            align: 'center',
            render: (_, record) => (
                <NumericFormat 
                    value={record.sueldo}
                    displayType={'text'}
                    thousandSeparator=','
                    prefix={'$'}
                    decimalScale={2}
                    fixedDecimalScale 
                />
            ),
        },
        {
            title: 'Acciones',
            key: 'acciones',
            align: 'center',
            render: (_, record) => (
                <Space>
                    <Link to={`/editar/${record.idEmpleado}`}>
                        <Button type="primary" size="small">Editar</Button>
                    </Link>
                    <Popconfirm
                        title="¿Estás seguro de eliminar este empleado?"
                        onConfirm={() => eliminarEmpleado(record.idEmpleado)}
                        okText="Sí"
                        cancelText="No"
                    >
                        <Button type="primary" danger size="small">Eliminar</Button>
                    </Popconfirm>
                </Space>
            ),
        },
    ];

    return (
        <div>
            <div style={{ textAlign: 'center', marginBottom: '30px' }}>
                <Title level={3}>Sistema de recursos humanos</Title>
            </div>

            <Table 
                columns={columns} 
                dataSource={empleados} 
                rowKey="idEmpleado"
                pagination={{ pageSize: 10 }}
            />
        </div>
    );
}

