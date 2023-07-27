import { mergeQueryKeys } from '@lukemorales/query-key-factory';

import { example1 } from './example1';
import { example2 } from './example2';

export const queryKeys = mergeQueryKeys(example1, example2);
