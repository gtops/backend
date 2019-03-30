import { ERoles } from "./ERoles";

export interface IRolesStore {
	[key: string]: ERoles[];
}
