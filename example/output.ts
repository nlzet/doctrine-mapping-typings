export type NlzetCustomAddress = {
    id: number;
    houseNumber?: string;
};

export type NlzetExamplePropertyTypes = {
    id: number;
    stringDefault: string;
    stringNullable?: string;
    integerDefault: number;
    integerNullable?: number;
    floatDefault: number;
    floatNullable?: number;
    decimalDefault: number;
    decimalNullable?: number;
    booleanDefault: boolean;
    booleanNullable?: boolean;
    datetimeDefault: any;
    datetimeNullable: any;
    timestampDefault: number;
    timestampNullable?: number;
    arrayDefault: any[];
    arrayNullable?: any[];
    simpleArrayDefault: any[];
    simpleArrayNullable?: any[];
    jsonDefault: any[];
    jsonNullable?: any[];
    objectDefault: any;
    objectNullable: any;
    blobDefault: any;
    blobNullable: any;
    guidDefault: string;
    guidNullable?: string;
    dateDefault: any;
    dateNullable: any;
    timeDefault: number;
    timeNullable?: number;
    datetimeImmutableDefault: any;
    datetimeImmutableNullable: any;
    timestampImmutableDefault: number;
    timestampImmutableNullable?: number;
    dateImmutableDefault: any;
    dateImmutableNullable: any;
    timeImmutableDefault: number;
    timeImmutableNullable?: number;
};

export type NlzetPerson = {
    id: number;
    name: string;
    extraData: any[];
    createdAt: any;
    updatedAt: any;
    createdDate: number;
    addresses: NlzetCustomAddress[];
};

